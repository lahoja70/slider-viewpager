import React, { useState, useEffect, useRef } from 'react';
import { useSprings, animated } from '@react-spring/web';
import useMeasure from 'react-use-measure';
import { useDrag } from 'react-use-gesture';
import clamp from 'lodash.clamp';
import styles from './styles.module.css';

const api = 'http://localhost/motionjs/motionphp/api.php';

interface ImageData {
  id: number;
  url: string;
  descripcion: string;
  width: number;
  height: number;
}

export default function App() {
  const [pages, setPages] = useState<string[]>([]);

  useEffect(() => {
    fetch(api)
      .then(response => {
        if (!response.ok) {
          throw new Error('Error al obtener los datos de la API');
        }
        return response.json();
      })
      .then(data => {
        setPages(data.slice(0, 3).map((image: ImageData) => image.url));
      })
      .catch(error => {
        console.error(error);
        // Manejar el error de manera adecuada, por ejemplo, mostrar un mensaje de error al usuario
      });
  }, []);

  function Viewpager() {
    const index = useRef(0);
    const [ref, { width }] = useMeasure();
    const [props, api] = useSprings(
      pages.length,
      i => ({
        x: i * width,
        scale: width === 0 ? 0 : 1,
        display: 'block',
      }),
      [width]
    );

    useEffect(() => {
      const intervalId = setInterval(() => {
        index.current = (index.current + 1) % pages.length;
        api.start(i => ({
          x: (i - index.current) * width,
          scale: 1,
          display: 'block',
        }));
      }, 3000);

      return () => clearInterval(intervalId);
    }, [width]);

    const bind = useDrag(({ active, movement: [mx], direction: [xDir], distance, cancel }) => {
      if (active && distance > width / 2) {
        index.current = clamp(index.current + (xDir > 0 ? -1 : 1), 0, pages.length - 1);
        cancel();
      }
      api.start(i => {
        if (i < index.current - 1 || i > index.current + 1) return { display: 'none' };
        const x = (i - index.current) * width + (active ? mx : 0);
        const scale = active ? 1 - distance / width / 2 : 1;
        return { x, scale, display: 'block' };
      });
    });

    return (
      <div ref={ref} className={styles.wrapper}>
        {props.map(({ x, display, scale }, i) => (
          <animated.div className={styles.page} {...bind()} key={i} style={{ display, x }}>
            <animated.div style={{ scale, backgroundImage: `url(${pages[i]})` }} />
          </animated.div>
        ))}
      </div>
    );
  }

  return (
    <div className={styles.container}>
      <Viewpager />
    </div>
  );
}
