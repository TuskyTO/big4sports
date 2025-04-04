import React, { useState } from 'react';
import { View, Text, Button, StyleSheet } from 'react-native';
import axios from 'axios';

export default function DeleteScreen({ route, navigation }) {
  const { id } = route.params;
  const [error, setError] = useState(null);

  const handleDelete = () => {
    // Send DELETE request
    axios
      .delete(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}`)
      .then((res) => {
        console.log('Delete response:', res.data);
        // Go back or navigate to your list screen
        navigation.navigate('Home');
      })
      .catch((err) => setError(err.message));
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Delete Trivia</Text>
      <Text>Are you sure you want to delete item #{id}?</Text>
      {error && <Text style={{ color: 'red' }}>Error: {error}</Text>}
      <Button title="Yes, Delete" onPress={handleDelete} />
      <Button title="Cancel" onPress={() => navigation.goBack()} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16 },
  title: { fontSize: 20, marginBottom: 8 },
});
